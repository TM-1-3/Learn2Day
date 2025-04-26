/** @jsx React.createElement */


// nem sei se vamos usar oopsie
interface CardProps {
    id?: string;
    title?: string;
    description?: string;
    imgSrc?: string;
    subtitles?: string[];
    //TODO: Add button prop
}

const Card = ({ id, title, description, imgSrc, subtitles}: CardProps) => {
    return (
        <div className="card" id={id}>
            <div className="container">
                <div className="details">
                    {imgSrc && <img className="img" src={imgSrc} alt={title || "Card image"} />}
                    {title && <h2 className="title">{title}</h2>}
                    {subtitles?.map((subtitle, idx) => (
                        <div key={idx} className="subtitles">
                            {subtitle}
                        </div>
                    ))}
                    {description && <p className="description">{description}</p>}
                </div>
            </div>
        </div>
    );
};